<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Tujuan Ambil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tambah Tujuan Ambil Baru</h1>
                        <p class="mt-1 text-sm text-gray-600">Masukkan informasi tujuan ambil yang akan ditambahkan</p>
                    </div>
                    <button type="button" onclick="window.close()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Tutup
                    </button>
                </div>
            </div>

            <!-- Form Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form action="{{ route('order.tujuan-ambil.store') }}" method="POST" class="space-y-6" id="tujuanAmbildForm">
                    @csrf

                    <!-- Kode Field -->
                    <div>
                        <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <input type="text" name="kode" id="kode" value="{{ old('kode') }}" required readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 @error('kode') border-red-500 @enderror"
                                   placeholder="Kode akan otomatis tergenerate">
                            <button type="button" id="generateKode" class="ml-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                Generate
                            </button>
                        </div>
                        @error('kode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Cabang Field -->
                        <div>
                            <label for="cabang" class="block text-sm font-medium text-gray-700 mb-2">
                                Cabang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="cabang" id="cabang" value="{{ old('cabang') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('cabang') border-red-500 @enderror"
                                   placeholder="Masukkan nama cabang">
                            @error('cabang')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Wilayah Field -->
                        <div>
                            <label for="wilayah" class="block text-sm font-medium text-gray-700 mb-2">
                                Wilayah <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="wilayah" id="wilayah" value="{{ old('wilayah') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('wilayah') border-red-500 @enderror"
                                   placeholder="Masukkan nama wilayah">
                            @error('wilayah')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Dari Field -->
                        <div>
                            <label for="dari" class="block text-sm font-medium text-gray-700 mb-2">
                                Dari <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="dari" id="dari" value="{{ old('dari') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('dari') border-red-500 @enderror"
                                   placeholder="Lokasi asal">
                            @error('dari')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ke Field -->
                        <div>
                            <label for="ke" class="block text-sm font-medium text-gray-700 mb-2">
                                Ke <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="ke" id="ke" value="{{ old('ke', $searchValue ?? '') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('ke') border-red-500 @enderror"
                                   placeholder="Lokasi tujuan">
                            @error('ke')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Keterangan Field -->
                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('keterangan') border-red-500 @enderror"
                                  placeholder="Masukkan keterangan (opsional)">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Field -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                            <option value="">Pilih Status</option>
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <button type="button" onclick="window.close()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Tujuan Ambil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-generate kode on page load
            generateKode();

            // Handle Generate Kode button
            document.getElementById('generateKode').addEventListener('click', function() {
                generateKode();
            });

            function generateKode() {
                fetch('{{ route("order.tujuan-ambil.store") }}', {
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
                    // Fallback: generate client-side code
                    const now = new Date();
                    const year = now.getFullYear().toString().substr(-2);
                    const month = (now.getMonth() + 1).toString().padStart(2, '0');
                    const day = now.getDate().toString().padStart(2, '0');
                    const time = now.getTime().toString().substr(-4);
                    const code = 'TA' + year + month + day + time.substr(0, 1);
                    document.getElementById('kode').value = code;
                });
            }

            // Handle form submission
            document.getElementById('tujuanAmbildForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitButton = this.querySelector('button[type="submit"]');
                
                // Disable submit button
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Replace entire body with success page
                    document.body.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan data');
                    
                    // Re-enable submit button
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save mr-2"></i>Simpan Tujuan Ambil';
                });
            });
        });
    </script>
</body>
</html>