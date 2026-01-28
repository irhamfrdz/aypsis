{{-- Partial: Field group for karyawan form (digunakan di create & onboarding) --}}

<fieldset class="{{ $fieldsetClasses }}">
    <legend class="{{ $legendClasses }}">
        <i class="fa fa-user mr-2 text-blue-600"></i> Informasi Pribadi
    </legend>

    <div class="mb-4 flex justify-end">
        @if(isset($karyawan) && $karyawan->exists)
            <a href="{{ route('karyawan.export-single', $karyawan->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" title="Export data karyawan ini ke Excel">
                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Data Excel
            </a>
        @else
            <a href="{{ route('karyawan.excel-template') }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" title="Download template untuk import data">
                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download Template Import
            </a>
        @endif
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="{{ $labelClasses }}" for="nik">NIK *</label>
            <input type="text" name="nik" id="nik" class="{{ $inputClasses }}" value="{{ old('nik', $karyawan->nik ?? '') }}" required placeholder="Masukkan NIK (angka saja, tanpa huruf)" pattern="[0-9]+">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="nama_lengkap">Nama Lengkap *</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" class="{{ $inputClasses }}" value="{{ old('nama_lengkap', $karyawan->nama_lengkap ?? '') }}" required>
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="nama_panggilan">Nama Panggilan *</label>
            <input type="text" name="nama_panggilan" id="nama_panggilan" class="{{ $inputClasses }}" value="{{ old('nama_panggilan', $karyawan->nama_panggilan ?? '') }}" required>
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="email">Email</label>
            <input type="email" name="email" id="email" class="{{ $inputClasses }}" value="{{ old('email', $karyawan->email ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="tanggal_lahir">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="{{ $inputClasses }}" value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="tempat_lahir">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" id="tempat_lahir" class="{{ $inputClasses }}" value="{{ old('tempat_lahir', $karyawan->tempat_lahir ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="jenis_kelamin">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="jenis_kelamin" class="{{ $selectClasses }}">
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="L" @if(old('jenis_kelamin', $karyawan->jenis_kelamin ?? '')=='L') selected @endif>Laki-laki</option>
                <option value="P" @if(old('jenis_kelamin', $karyawan->jenis_kelamin ?? '')=='P') selected @endif>Perempuan</option>
            </select>
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="agama">Agama</label>
            <select name="agama" id="agama" class="{{ $selectClasses }}">
                <option value="">-- Pilih Agama --</option>
                <option value="Islam" @if(old('agama', $karyawan->agama ?? '')=='Islam') selected @endif>Islam</option>
                <option value="Kristen" @if(old('agama', $karyawan->agama ?? '')=='Kristen') selected @endif>Kristen</option>
                <option value="Katolik" @if(old('agama', $karyawan->agama ?? '')=='Katolik') selected @endif>Katolik</option>
                <option value="Hindu" @if(old('agama', $karyawan->agama ?? '')=='Hindu') selected @endif>Hindu</option>
                <option value="Buddha" @if(old('agama', $karyawan->agama ?? '')=='Buddha') selected @endif>Buddha</option>
                <option value="Konghucu" @if(old('agama', $karyawan->agama ?? '')=='Konghucu') selected @endif>Konghucu</option>
            </select>
        </div>
        <!-- Tambahkan field lain sesuai kebutuhan -->
    </div>
</fieldset>
