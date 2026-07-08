<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Penerima</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-4">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b border-gray-200 p-4 bg-gradient-to-r from-amber-500 to-amber-600">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-user-edit mr-2"></i>
                Edit Penerima
            </h2>
        </div>

        <form action="{{ route('tanda-terima.penerima.update', $penerima->id) }}" method="POST" class="p-6 space-y-4" id="penerimaForm">
            @csrf
            @method('PUT')
            
            <!-- Hidden field to mark as popup -->
            <input type="hidden" name="popup" value="1">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nama Penerima -->
                <div class="md:col-span-2">
                    <label for="nama_penerima" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Penerima <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nama_penerima" 
                           name="nama_penerima" 
                           value="{{ old('nama_penerima', $penerima->nama_penerima) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 @error('nama_penerima') border-red-500 @enderror"
                           placeholder="Masukkan nama penerima"
                           required
                           autofocus>
                    @error('nama_penerima')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- PIC -->
                <div class="md:col-span-1">
                    <label for="pic" class="block text-sm font-medium text-gray-700 mb-2">
                        PIC
                    </label>
                    <input type="text" 
                           id="pic" 
                           name="pic" 
                           value="{{ old('pic', $penerima->pic) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 @error('pic') border-red-500 @enderror"
                           placeholder="Masukkan PIC">
                    @error('pic')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Telepon -->
                <div class="md:col-span-1">
                    <label for="telepon" class="block text-sm font-medium text-gray-700 mb-2">
                        No. Telepon
                    </label>
                    <input type="text" 
                           id="telepon" 
                           name="telepon" 
                           value="{{ old('telepon', $penerima->telepon) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 @error('telepon') border-red-500 @enderror"
                           placeholder="Masukkan nomor telepon">
                    @error('telepon')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Alamat -->
                <div class="md:col-span-2">
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat
                    </label>
                    <textarea id="alamat" 
                              name="alamat" 
                              rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 @error('alamat') border-red-500 @enderror"
                              placeholder="Masukkan alamat lengkap (opsional)">{{ old('alamat', $penerima->alamat) }}</textarea>
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
                           value="{{ old('npwp', $penerima->npwp) }}"
                           maxlength="20"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 @error('npwp') border-red-500 @enderror"
                           placeholder="Masukkan NPWP">
                    @error('npwp')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- NITKU -->
                <div>
                    <label for="nitku" class="block text-sm font-medium text-gray-700 mb-2">
                        NITKU
                    </label>
                    <input type="text" 
                           id="nitku" 
                           name="nitku" 
                           value="{{ old('nitku', $penerima->nitku) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 @error('nitku') border-red-500 @enderror"
                           placeholder="Masukkan NITKU">
                    @error('nitku')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- IU BP Kawasan -->
                <div>
                    <label for="iu_bp_kawasan" class="block text-sm font-medium text-gray-700 mb-2">
                        IU BP Kawasan
                    </label>
                    <select id="iu_bp_kawasan" 
                            name="iu_bp_kawasan" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
                        <option value="tidak ada" {{ old('iu_bp_kawasan', $penerima->iu_bp_kawasan) == 'tidak ada' ? 'selected' : '' }}>Tidak Ada</option>
                        <option value="ada" {{ old('iu_bp_kawasan', $penerima->iu_bp_kawasan) == 'ada' ? 'selected' : '' }}>Ada</option>
                    </select>
                </div>

                <!-- Status (Hidden - keep existing) -->
                <input type="hidden" name="status" value="{{ $penerima->status }}">

                <!-- Catatan -->
                <div class="md:col-span-2">
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea id="catatan" 
                              name="catatan" 
                              rows="2"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 @error('catatan') border-red-500 @enderror"
                              placeholder="Masukkan catatan tambahan">{{ old('catatan', $penerima->catatan) }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" 
                        onclick="window.close()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-amber-600 border border-transparent rounded-md hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Perubahan
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

    @if (session('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-md z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Focus on the nama input when page loads
            const namaInput = document.getElementById('nama_penerima');
            if (namaInput) {
                setTimeout(() => {
                    namaInput.focus();
                    namaInput.select();
                }, 100);
            }

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
                if (window.opener && !window.opener.closed) {
                    const penerimaData = {
                        type: 'penerimaAdded', // Use the same type so it refreshes the dropdown
                        penerima: {
                            nama: '{{ session("penerima_nama") }}',
                            alamat: '{{ session("penerima_alamat") ?? "" }}'
                        }
                    };
                    
                    console.log('Sending updated penerima data to parent:', penerimaData);
                    window.opener.postMessage(penerimaData, window.location.origin);
                    
                    // Close popup immediately
                    setTimeout(function() {
                        window.close();
                    }, 500);
                } else {
                    console.error('window.opener not available or closed');
                    alert('Data berhasil disimpan! Silakan tutup jendela ini secara manual.');
                }
            @endif
        });
    </script>
</body>
</html>
