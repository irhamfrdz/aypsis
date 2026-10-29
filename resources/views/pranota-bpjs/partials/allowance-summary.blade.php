<section class="my-5 rounded-xl border border-orange-200 bg-orange-50/40 p-4">
    <h3 class="font-semibold text-gray-800">Ringkasan Tunjangan</h3>
    <p class="mt-1 mb-3 text-xs text-gray-500">Nominal per karyawan dapat diubah pada kolom Ringkasan Tunjangan di form. Pilih Auto untuk kembali ke rumus. Perubahan ringkasan tidak mengubah komponen iuran atau Grand Total.</p>
    <p class="mb-3 text-xs text-gray-500">Khusus tipe BP Jamsostek NON KARY-UMKM Non PP, Beban PT = JHT 2% (BIAYA) + JKK 1% (TUNJ) + JKM (Rp).</p>
    <p class="mb-3 text-xs text-gray-500">Untuk tipe NON KARY, Beban PT mengambil TOTAL JKN pada group JKN-KIS dan TOTAL PPU pada group PPU. Jika kedua keanggotaan memenuhi syarat, kedua total dijumlahkan.</p>
    <table class="w-full text-sm">
        <thead><tr class="border-b border-orange-200 text-gray-600"><th class="py-2 text-left">Komponen</th><th class="py-2 text-right">Total (Rp)</th></tr></thead>
        <tbody>
            @foreach(['ppu' => 'Tunjangan JKK 0,24% + JKM 0,3%', 'bpu' => 'Tunjangan BPU JKK 1% + JKM', 'jkn' => 'Tunjangan JKN 4%', 'kary_jht' => 'Beban KARY. JHT 2%, JP 1% & BPU JHT', 'kary_jkn' => 'Beban KARY. JKN 1%', 'pt' => 'Beban PT. PPU JHT 3.7%, PPU JP 2% & JHT 2% (BIAYA)', 'non_kary' => 'Beban NON KARY. JKN & BPJS'] as $key => $label)
                <tr class="border-b border-orange-100" data-allowance-component="{{ $key }}"><td class="py-2">{{ $label }}</td><td class="py-2 text-right font-mono font-semibold" data-allowance-total="{{ $key }}">Rp {{ number_format($allowanceSummary[$key] ?? 0, 2, ',', '.') }}</td></tr>
            @endforeach
        </tbody>
    </table>
</section>
