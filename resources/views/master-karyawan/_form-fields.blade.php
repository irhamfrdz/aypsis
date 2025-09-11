{{-- Partial: Field group for karyawan form (digunakan di create & onboarding) --}}

<fieldset class="{{ $fieldsetClasses }}">
    <legend class="{{ $legendClasses }}">
        <i class="fa fa-user mr-2 text-blue-600"></i> Informasi Pribadi
    </legend>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="{{ $labelClasses }}" for="nik">NIK *</label>
            <input type="text" name="nik" id="nik" class="{{ $inputClasses }}" value="{{ old('nik', $karyawan->nik ?? '') }}" required>
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
