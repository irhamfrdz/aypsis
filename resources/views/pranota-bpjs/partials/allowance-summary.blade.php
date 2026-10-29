<section class="my-5 rounded-xl border border-orange-200 bg-orange-50/40 p-4">
    <h3 class="font-semibold text-gray-800">Ringkasan Tunjangan</h3>
    <p class="mt-1 mb-3 text-xs text-gray-500">Nominal per karyawan dapat diubah pada kolom Ringkasan Tunjangan di form. Pilih Auto untuk kembali ke rumus. Perubahan ringkasan tidak mengubah komponen iuran atau Grand Total.</p>
    <p class="mb-3 text-xs text-gray-500">Tunjangan JKN 4% tidak menghitung karyawan dengan tipe JKN NON KARY.</p>
    <p class="mb-3 text-xs text-gray-500">Tunjangan JKK 0,24% + JKM 0,3% tidak menghitung karyawan dengan tipe BP Jamsostek NON KARY.</p>
    <p class="mb-3 text-xs text-gray-500">Group BP Jamsostek CILANDAK-HL dengan tipe NON KARY-UMKM NON PP tidak dihitung pada Beban PT, termasuk nominal manual yang tersimpan. Group lain, termasuk BPU-SUPIR, tetap menghitung komponen biaya JHT dan JP yang berlaku.</p>
    <p class="mb-3 text-xs text-gray-500">Tipe NON KARY-UMKM non PP tidak dihitung pada Tunjangan BPU JKK 1% + JKM.</p>
    <p class="mb-3 text-xs text-gray-500">Beban Karyawan JHT 2%, JP 1% &amp; BPU JHT tidak menghitung tipe BP Jamsostek NON KARY dan NON KARY-UMKM NON PP.</p>
    <p class="mb-3 text-xs text-gray-500">Untuk tipe BP Jamsostek NON KARY-UMKM NON PP, JHT 2% (HUTANG) masuk ke Beban Non Kary. JKN &amp; BPJS.</p>
    <p class="mb-3 text-xs text-gray-500">Beban PT dihitung dari komponen BP Jamsostek yang berlaku, termasuk untuk Group JKN JKN-KIS. Untuk Group BP Jamsostek PPU dan Tipe NON KARY, Beban PT menggunakan TOTAL PPU.</p>
    <table class="w-full text-sm">
        <thead><tr class="border-b border-orange-200 text-gray-600"><th class="py-2 text-left">Komponen</th><th class="py-2 text-right">Total (Rp)</th></tr></thead>
        <tbody>
            @foreach(['ppu' => 'Tunjangan JKK 0,24% + JKM 0,3%', 'bpu' => 'Tunjangan BPU JKK 1% + JKM', 'jkn' => 'Tunjangan JKN 4%', 'kary_jht' => 'Beban KARY. JHT 2%, JP 1% & BPU JHT', 'kary_jkn' => 'Beban KARY. JKN 1%', 'pt' => 'Beban PT. PPU JHT 3.7%, PPU JP 2% & JHT 2% (BIAYA)', 'non_kary' => 'Beban NON KARY. JKN & BPJS'] as $key => $label)
                <tr class="border-b border-orange-100" data-allowance-component="{{ $key }}"><td class="py-2">{{ $label }}</td><td class="py-2 text-right font-mono font-semibold" data-allowance-total="{{ $key }}">Rp {{ number_format($allowanceSummary[$key] ?? 0, 2, ',', '.') }}</td></tr>
            @endforeach
        </tbody>
    </table>
</section>
