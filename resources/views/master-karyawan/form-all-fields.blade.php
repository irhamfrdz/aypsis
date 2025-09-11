{{-- Partial: Semua field form karyawan, untuk onboarding-full dan master-karyawan.create --}}
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
        <div>
            <label class="{{ $labelClasses }}" for="alamat">Alamat</label>
            <input type="text" name="alamat" id="alamat" class="{{ $inputClasses }}" value="{{ old('alamat', $karyawan->alamat ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="rt_rw">RT/RW</label>
            <input type="text" name="rt_rw" id="rt_rw" class="{{ $inputClasses }}" value="{{ old('rt_rw', $karyawan->rt_rw ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="kelurahan">Kelurahan</label>
            <input type="text" name="kelurahan" id="kelurahan" class="{{ $inputClasses }}" value="{{ old('kelurahan', $karyawan->kelurahan ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="kecamatan">Kecamatan</label>
            <input type="text" name="kecamatan" id="kecamatan" class="{{ $inputClasses }}" value="{{ old('kecamatan', $karyawan->kecamatan ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="kabupaten">Kabupaten</label>
            <input type="text" name="kabupaten" id="kabupaten" class="{{ $inputClasses }}" value="{{ old('kabupaten', $karyawan->kabupaten ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="provinsi">Provinsi</label>
            <input type="text" name="provinsi" id="provinsi" class="{{ $inputClasses }}" value="{{ old('provinsi', $karyawan->provinsi ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="kode_pos">Kode Pos</label>
            <input type="text" name="kode_pos" id="kode_pos" class="{{ $inputClasses }}" value="{{ old('kode_pos', $karyawan->kode_pos ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="alamat_lengkap">Alamat Lengkap</label>
            <input type="text" name="alamat_lengkap" id="alamat_lengkap" class="{{ $inputClasses }}" value="{{ old('alamat_lengkap', $karyawan->alamat_lengkap ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="no_hp">No HP</label>
            <input type="text" name="no_hp" id="no_hp" class="{{ $inputClasses }}" value="{{ old('no_hp', $karyawan->no_hp ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="status_perkawinan">Status Perkawinan</label>
            <input type="text" name="status_perkawinan" id="status_perkawinan" class="{{ $inputClasses }}" value="{{ old('status_perkawinan', $karyawan->status_perkawinan ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="divisi">Divisi</label>
            <input type="text" name="divisi" id="divisi" class="{{ $inputClasses }}" value="{{ old('divisi', $karyawan->divisi ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="pekerjaan">Pekerjaan</label>
            <input type="text" name="pekerjaan" id="pekerjaan" class="{{ $inputClasses }}" value="{{ old('pekerjaan', $karyawan->pekerjaan ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="plat">Plat</label>
            <input type="text" name="plat" id="plat" class="{{ $inputClasses }}" value="{{ old('plat', $karyawan->plat ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="ktp">No KTP</label>
            <input type="text" name="ktp" id="ktp" class="{{ $inputClasses }}" value="{{ old('ktp', $karyawan->ktp ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="kk">No KK</label>
            <input type="text" name="kk" id="kk" class="{{ $inputClasses }}" value="{{ old('kk', $karyawan->kk ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="status_pajak">Status Pajak</label>
            <input type="text" name="status_pajak" id="status_pajak" class="{{ $inputClasses }}" value="{{ old('status_pajak', $karyawan->status_pajak ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="nama_bank">Nama Bank</label>
            <input type="text" name="nama_bank" id="nama_bank" class="{{ $inputClasses }}" value="{{ old('nama_bank', $karyawan->nama_bank ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="bank_cabang">Bank Cabang</label>
            <input type="text" name="bank_cabang" id="bank_cabang" class="{{ $inputClasses }}" value="{{ old('bank_cabang', $karyawan->bank_cabang ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="akun_bank">Akun Bank</label>
            <input type="text" name="akun_bank" id="akun_bank" class="{{ $inputClasses }}" value="{{ old('akun_bank', $karyawan->akun_bank ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="atas_nama">Atas Nama</label>
            <input type="text" name="atas_nama" id="atas_nama" class="{{ $inputClasses }}" value="{{ old('atas_nama', $karyawan->atas_nama ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="jkn">JKN</label>
            <input type="text" name="jkn" id="jkn" class="{{ $inputClasses }}" value="{{ old('jkn', $karyawan->jkn ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="no_ketenagakerjaan">No Ketenagakerjaan</label>
            <input type="text" name="no_ketenagakerjaan" id="no_ketenagakerjaan" class="{{ $inputClasses }}" value="{{ old('no_ketenagakerjaan', $karyawan->no_ketenagakerjaan ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="cabang">Cabang</label>
            <input type="text" name="cabang" id="cabang" class="{{ $inputClasses }}" value="{{ old('cabang', $karyawan->cabang ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="nik_supervisor">NIK Supervisor</label>
            <input type="text" name="nik_supervisor" id="nik_supervisor" class="{{ $inputClasses }}" value="{{ old('nik_supervisor', $karyawan->nik_supervisor ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="supervisor">Supervisor</label>
            <input type="text" name="supervisor" id="supervisor" class="{{ $inputClasses }}" value="{{ old('supervisor', $karyawan->supervisor ?? '') }}">
        </div>
        <div>
            <label class="{{ $labelClasses }}" for="catatan">Catatan</label>
            <input type="text" name="catatan" id="catatan" class="{{ $inputClasses }}" value="{{ old('catatan', $karyawan->catatan ?? '') }}">
        </div>
    </div>
</fieldset>
