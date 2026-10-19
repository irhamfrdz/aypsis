<section id="ranking-armada" class="space-y-4">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Ranking Utilisasi Armada</h2>
        <p class="mt-1 text-sm text-gray-500">Utilisasi = hari menghasilkan rit ÷ hari tersedia × 100%. Seluruh armada hasil filter mendapat satu urutan peringkat 1, 2, 3 tanpa nomor ganda.</p>
        @if($rankingSummary['provisional_vehicles'] > 0)
            <p class="mt-2 rounded-lg bg-amber-50 border border-amber-200 p-3 text-sm text-amber-800">{{ $rankingSummary['provisional_vehicles'] }} armada memiliki ranking sementara berdasarkan rit yang tercatat. Sebagian data GPS pada hasil filter belum lengkap; posisi masih dapat berubah setelah data dilengkapi.</p>
        @endif
    </div>
    <nav aria-label="Mode tampilan peringkat" class="inline-flex gap-1 rounded-lg border border-gray-200 bg-white p-1">
        @foreach(['tabel' => 'Tabel', 'diagram' => 'Diagram'] as $mode => $label)
            <a href="{{ route('dashboard-armada.index', array_merge(request()->query(), ['mode' => $mode])) }}#ranking-armada" @if($rankingMode === $mode) aria-current="page" @endif class="rounded-md px-4 py-2 text-sm font-medium transition-colors {{ $rankingMode === $mode ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">{{ $label }}</a>
        @endforeach
    </nav>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach([
            ['Hari Menghasilkan Rit', $rankingSummary['productive_days']],
            ['Hari Bergerak Tanpa Rit Selesai', $rankingSummary['moving_without_rit_days']],
            ['Hari Tanpa Aktivitas Tercatat', $rankingSummary['idle_days']],
            ['Hari Data Belum Lengkap', $rankingSummary['unknown_days']],
        ] as [$label, $value])
            <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-xs text-gray-500">{{ $label }}</p><p class="mt-2 text-2xl font-bold text-indigo-700">{{ $value }}</p></div>
        @endforeach
    </div>
    <p class="text-xs text-gray-500">Angka hari di atas dijumlahkan untuk seluruh armada hasil filter. Total hari tersedia: {{ $rankingSummary['available_days'] }}; hari tidak tersedia yang dikecualikan: {{ $rankingSummary['unavailable_days'] }}. Jarak GPS tercatat pada hari tersedia: {{ number_format($rankingSummary['gps_km'], 3, ',', '.') }} km.</p>
    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        @if($rankingMode === 'diagram')
            @include('dashboard-armada._ranking-diagram')
        @else
        @include('dashboard-armada._ranking-table')
        @endif
        <div class="border-t border-gray-200 p-4 text-xs text-gray-500 space-y-2">
            <p>Hari kalender dianggap tersedia kecuali ada catatan tidak tersedia. Catat servis, libur operasional, atau kondisi lain yang membuat armada tidak dapat ditugaskan. Periode tumpang tindih dihitung sekali.</p>
            <p>Hari menghasilkan rit dihitung dari tanggal tanda terima pertama: 1 surat jalan = 1 rit. Surat jalan batal atau tanpa uang rit dikecualikan. Hari bergerak tanpa rit dapat merupakan perjalanan kosong atau pekerjaan yang belum selesai; hari tanpa aktivitas adalah potensi menganggur berdasarkan data tercatat.</p>
            <p>GPS kosong, jarak negatif, atau rekaman tanpa titik GPS bukan bukti kendaraan diam. Jika ada rit atau data GPS pada periode ini, peringkat berdasarkan rit tercatat tetap ditampilkan. Seluruh peringkat hasil filter diberi label Sementara ketika data GPS salah satu armadanya belum lengkap. Hari tanpa data tetap masuk hari tersedia dan tidak dianggap hari tanpa aktivitas.</p>
            <p>Plat kosong/duplikat, tidak tersedia selama seluruh periode, catatan tidak tersedia yang bertentangan dengan aktivitas, atau belum adanya data aktivitas periode tetap menahan ranking. Ranking sementara tidak membuktikan armada paling efisien atau paling menganggur.</p>
            <p>Peringkat diurutkan dari utilisasi tertinggi, lalu rit per hari tersedia; hasil sama diurutkan menurut nomor polisi dan ID. Nomor peringkat unik dihitung sebelum pagination. Gunakan filter lokasi dan jenis kendaraan untuk membandingkan armada yang sebanding; rute, muatan, dan durasi pekerjaan tetap perlu ditinjau. Km awal tetap tersimpan di Master Armada dan tidak digunakan untuk menilai utilisasi.</p>
        </div>
        @if($rankings->hasPages())<div class="border-t border-gray-200 px-5 py-4">{{ $rankings->links() }}</div>@endif
    </div>
</section>
