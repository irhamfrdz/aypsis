<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Tujuan Kirim</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Tambah Tujuan Kirim</h1>
            <p class="mt-1 text-sm text-gray-600">Buat data tujuan pengiriman kontainer baru</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Form Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('tujuan-kirim.store') }}" method="POST" id="tujuanKirimForm">
                @csrf
                <input type="hidden" name="popup" value="1">

                <div class="space-y-6">
                    <!-- Nama Tujuan -->
                    <div>
                        <label for="nama_tujuan" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Tujuan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="nama_tujuan"
                               id="nama_tujuan"
                               value="{{ old('nama_tujuan', request('search')) }}"
                               list="tujuan_suggestions"
                               maxlength="100"
                               required
                               placeholder="Contoh: Jakarta Pusat"
                               autocomplete="off"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 transition-colors duration-200 @error('nama_tujuan') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @enderror">
                        <datalist id="tujuan_suggestions"></datalist>
                        @error('nama_tujuan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Nama lengkap tujuan pengiriman maksimal 100 karakter</p>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan
                        </label>
                        <textarea name="catatan"
                                  id="catatan"
                                  rows="3"
                                  maxlength="500"
                                  placeholder="Masukkan catatan tambahan jika diperlukan..."
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 transition-colors duration-200 resize-none @error('catatan') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @enderror">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Informasi tambahan maksimal 500 karakter (opsional)</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status"
                                id="status"
                                required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 transition-colors duration-200 @error('status') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @enderror">
                            <option value="">Pilih Status</option>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                Aktif
                            </option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                Tidak Aktif
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" onclick="window.close()" class="px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-focus on nama_tujuan field if there's a search term
        const namaTujuanField = document.getElementById('nama_tujuan');
        const searchTerm = '{{ request("search") }}';

        if (namaTujuanField && searchTerm) {
            namaTujuanField.focus();
            namaTujuanField.select();
        }

        // Handle form submission
        document.getElementById('tujuanKirimForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            
            // Disable button and show loading
            submitButton.disabled = true;
            submitButton.textContent = 'Menyimpan...';

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                // Check if response is ok
                if (!response.ok) {
                    return response.json().then(err => {
                        throw err;
                    }).catch(() => {
                        throw new Error(`HTTP error! Status: ${response.status} - ${response.statusText}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Send message to parent window
                    if (window.opener) {
                        window.opener.postMessage({
                            type: 'tujuan-kirim-added',
                            data: data.data
                        }, '*');
                    }

                    // Show success message
                    alert('✅ Tujuan Kirim berhasil ditambahkan!');

                    // Close popup
                    window.close();
                } else {
                    // Handle validation errors
                    let errorMessage = 'Gagal menambahkan tujuan kirim:\n\n';
                    
                    if (data.errors) {
                        // Laravel validation errors
                        Object.keys(data.errors).forEach(key => {
                            errorMessage += `• ${data.errors[key].join(', ')}\n`;
                        });
                    } else if (data.message) {
                        errorMessage += data.message;
                    } else {
                        errorMessage += 'Terjadi kesalahan yang tidak diketahui';
                    }
                    
                    alert(errorMessage);
                    
                    // Re-enable button
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                let errorMessage = '❌ Gagal menyimpan data:\n\n';
                
                if (error.errors) {
                    // Laravel validation errors
                    Object.keys(error.errors).forEach(key => {
                        errorMessage += `• ${key}: ${error.errors[key].join(', ')}\n`;
                    });
                } else if (error.message) {
                    if (error.message.includes('HTTP error')) {
                        errorMessage += `Server error: ${error.message}\n\n`;
                        errorMessage += 'Kemungkinan penyebab:\n';
                        errorMessage += '• Koneksi ke server terputus\n';
                        errorMessage += '• Server sedang sibuk\n';
                        errorMessage += '• Session telah habis (silakan refresh halaman)';
                    } else if (error.message.includes('Failed to fetch')) {
                        errorMessage += 'Tidak dapat terhubung ke server.\n\n';
                        errorMessage += 'Kemungkinan penyebab:\n';
                        errorMessage += '• Koneksi internet terputus\n';
                        errorMessage += '• Server tidak merespons\n';
                        errorMessage += '• Firewall memblokir koneksi';
                    } else {
                        errorMessage += error.message;
                    }
                } else {
                    errorMessage += 'Terjadi kesalahan yang tidak diketahui.\n';
                    errorMessage += 'Silakan coba lagi atau hubungi administrator.';
                }
                
                alert(errorMessage);
                
                // Re-enable button
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            });
        });

        // Character counter for catatan
        const catatanField = document.getElementById('catatan');
        if (catatanField) {
            const maxLength = 500;

            // Create counter element
            const counter = document.createElement('div');
            counter.className = 'text-right text-xs text-gray-400 mt-1';
            counter.id = 'catatan-counter';
            catatanField.parentNode.appendChild(counter);

            function updateCounter() {
                const currentLength = catatanField.value.length;
                counter.textContent = `${currentLength}/${maxLength} karakter`;

                if (currentLength > maxLength * 0.9) {
                    counter.className = 'text-right text-xs text-orange-500 mt-1';
                } else if (currentLength === maxLength) {
                    counter.className = 'text-right text-xs text-red-500 mt-1';
                } else {
                    counter.className = 'text-right text-xs text-gray-400 mt-1';
                }
            }

            catatanField.addEventListener('input', updateCounter);
            updateCounter(); // Initialize counter
        }

        // Suggestions for existing Nama Tujuan to avoid duplicates
        (function() {
            const tujuanInput = document.getElementById('nama_tujuan');
            const suggestionsList = document.getElementById('tujuan_suggestions');
            let suggestionTimer = null;
            let lastSuggestions = [];

            function fetchSuggestions(q) {
                fetch(`{{ route('order.tujuan-kirim.suggest') }}?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        suggestionsList.innerHTML = '';
                        lastSuggestions = data || [];
                        (data || []).forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item;
                            suggestionsList.appendChild(opt);
                        });
                    }).catch(() => {
                        // ignore
                    });
            }

            if (tujuanInput) {
                tujuanInput.addEventListener('input', function() {
                    const q = this.value.trim();
                    if (suggestionTimer) clearTimeout(suggestionTimer);
                    if (!q) { suggestionsList.innerHTML = ''; lastSuggestions = []; return; }
                    suggestionTimer = setTimeout(() => fetchSuggestions(q), 250);
                });

                // On submit, warn user if exact name exists in suggestions
                const form = document.getElementById('tujuanKirimForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const val = tujuanInput.value.trim();
                        if (!val) return;
                        const exists = lastSuggestions.some(s => s.toLowerCase() === val.toLowerCase());
                        if (exists) {
                            if (!confirm('Nama tujuan kirim sudah ada. Tetap tambahkan data baru?')) {
                                e.preventDefault();
                                tujuanInput.focus();
                                return;
                            }
                        }
                    });
                }
            }
        })();
    </script>
</body>
</html>
