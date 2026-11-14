<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use Tests\TestCase;

class PerhitunganLemburViewTest extends TestCase
{
    private function data(): array
    {
        return collect([['partial', 1, 2], ['complete', 2, 2], ['unpaid', 0, 2]])->mapWithKeys(function ($status, $index) {
            [$state, $paid, $total] = $status;
            return [$index + 1 => [
                'karyawan' => (new Karyawan)->forceFill([
                    'id' => $index + 1, 'nama_lengkap' => 'Karyawan Contoh '.$index,
                    'nik' => '000'.$index, 'penempatan' => 'JAKARTA PELABUHAN',
                ]),
                'total_jam_biasa' => 4, 'total_jam_libur' => 6, 'total_nominal' => 100000,
                'total_uang_makan_lembur' => 65000,
                'is_all_in_pranota' => $state === 'complete', 'is_partial_in_pranota' => $state === 'partial',
                'pranota_dates_count' => $paid, 'total_dates_count' => $total,
                'pranota_nomors' => $paid ? ['PL-TEST'] : [],
                'unpranota_jam' => 6, 'unpranota_nominal' => 50000,
                'unpranota_uml' => 30000, 'unpranota_grand_total' => 80000,
                'daftar_hari_lembur' => [
                    ['label' => 'Sen, 2 Nov', 'hari' => 'Senin', 'tanggal' => '2026-11-02', 'durasi_jam' => 4],
                    ['label' => 'Min, 8 Nov', 'hari' => 'Minggu', 'tanggal' => '2026-11-08', 'durasi_jam' => 6],
                ],
                'detail' => [['tanggal' => '2026-11-02', 'durasi_jam' => 4, 'nominal' => 50000]],
            ]];
        })->all();
    }

    private function render(array $data, array $history = []): array
    {
        view()->share('errors', new \Illuminate\Support\ViewErrorBag);
        $html = view('payroll.perhitungan-lembur.index', [
            'rekapData' => $data, 'startDateStr' => '2026-11-01', 'endDateStr' => '2026-11-30',
            'grupsList' => ['UANG LEMBUR'], 'grupMap' => [], 'riwayatPranotaUser' => collect($history),
            'cabangs' => collect(['JAKARTA', 'BATAM']),
        ])->render();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);

        return [$html, new \DOMXPath($dom)];
    }

    public function test_partial_and_complete_statuses_and_remaining_payment_stay_visible(): void
    {
        [$html, $xpath] = $this->render($this->data());
        $partial = $xpath->query('//div[contains(@class,"ot-status-partial")]');
        $this->assertSame(1, $partial->length);
        $this->assertStringContainsString('Sebagian di Pranota (1/2 hari)', $partial->item(0)->textContent);
        $this->assertSame(1, $xpath->query('//div[contains(@class,"ot-status-complete")]')->length);
        $this->assertStringContainsString('Sudah Masuk Pranota (PL-TEST)', $html);
        $this->assertSame('Sisa: Rp 80.000', trim($xpath->query('//div[contains(@class,"ot-remaining")]')->item(0)->textContent));
        $this->assertSame('Rp 165.000', trim($xpath->query('//span[@class="ot-payout-amount"]')->item(0)->textContent));
        $this->assertSame(3, $xpath->query('//table[contains(@class,"ot-table")]/tbody/tr')->length);
        $this->assertSame(9, $xpath->query('//table[contains(@class,"ot-table")]/thead/tr/th')->length);
        $this->assertSame(3, $xpath->query('//span[@class="ot-avatar"]')->length);
        $this->assertStringContainsString('2 Hari Lembur', $html);
    }

    public function test_checkbox_amounts_detail_payload_and_filter_bindings_are_preserved(): void
    {
        [$html, $xpath] = $this->render($this->data());
        $checkboxes = $xpath->query('//input[contains(@class,"row-checkbox")]');
        $this->assertSame(3, $checkboxes->length);
        $this->assertSame('80000', $checkboxes->item(0)->getAttribute('data-nominal-awal'));
        $this->assertSame('165000', $checkboxes->item(1)->getAttribute('data-nominal-awal'));
        $this->assertSame('6', $checkboxes->item(0)->getAttribute('data-jam-lembur'));
        $details = $xpath->query('//button[contains(@class,"btn-detail")]');
        $this->assertSame(3, $details->length);
        $this->assertSame('2026-11-02', json_decode($details->item(0)->getAttribute('data-detail'), true)[0]['tanggal']);
        $this->assertSame('Karyawan Contoh 0', trim($xpath->query('//div[contains(@class,"ot-employee-name")]')->item(0)->textContent));
        foreach (['search', 'grup', 'sub_grup', 'status_pranota', 'cabang', 'start_date', 'end_date'] as $field) {
            $this->assertSame(1, $xpath->query('//*[@id="filterForm"]//*[@name="'.$field.'"]')->length);
        }
        $this->assertSame(route('payroll.perhitungan-lembur'), $xpath->query('//*[@id="filterForm"]')->item(0)->getAttribute('action'));
        $this->assertSame('handleGrupFilterChange()', $xpath->query('//*[@id="grup"]')->item(0)->getAttribute('onchange'));
        $this->assertSame(2, $xpath->query('//button[@onclick="window.location.reload()"]')->length);
        $this->assertSame(1, $xpath->query('//*[@id="quick-karyawan-search"]')->length);
        $this->assertStringContainsString('css/payroll-perhitungan-lembur.css?v=', $html);
    }

    public function test_empty_overtime_results_render_safely(): void
    {
        [$html, $xpath] = $this->render([]);
        $this->assertStringContainsString('Tidak ada data lembur', $html);
        $this->assertSame('9', $xpath->query('//table[contains(@class,"ot-table")]/tbody/tr/td')->item(0)->getAttribute('colspan'));
        $this->assertSame(0, $xpath->query('//div[contains(@class,"ot-status-partial")]')->length);
    }

    public function test_history_modal_preserves_amounts_employee_details_and_locked_actions(): void
    {
        $creator = (new \App\Models\User)->forceFill(['username' => 'NAMA PEMBUAT PRANOTA YANG SANGAT PANJANG'])->setRelation('karyawan', null);
        $history = collect([[101, null, 190000], [102, 'approved', 0], [103, 'draft', -5000]])->map(function ($values) use ($creator) {
            [$id, $pumlStatus, $adjustment] = $values;
            $detail = (new \App\Models\PranotaLemburKaryawan)->forceFill([
                'id' => $id, 'karyawan_id' => 1, 'jam_lembur' => 4, 'nominal_awal' => 1000000,
                'adjustment' => $adjustment, 'total_akhir' => 1000000 + $adjustment,
                'tanggal_lembur' => ['2026-11-02'],
            ])->setRelation('karyawan', (new Karyawan)->forceFill(['nama_lengkap' => 'Karyawan Satu', 'nik' => '0001']));
            return (new \App\Models\PranotaLemburKaryawanHeader)->forceFill([
                'id' => $id, 'nomor_pranota' => 'PML-TEST-'.$id, 'tanggal_pranota' => '2026-11-10',
                'created_at' => now(), 'total_biaya' => 1000000, 'adjustment' => $adjustment,
                'total_setelah_adjustment' => 1000000 + $adjustment, 'karyawans_count' => 1,
                'status' => 'draft', 'pranota_puml_id' => $pumlStatus ? $id : null,
            ])->setRelation('karyawans', collect([$detail]))
                ->setRelation('creator', $creator)
                ->setRelation('pranotaPuml', $pumlStatus ? (new \App\Models\PranotaPuml)->forceFill(['status' => $pumlStatus]) : null);
        })->all();
        [$html, $xpath] = $this->render([], $history);
        $modal = '//*[@id="riwayat-pranota-modal"]';
        $this->assertSame(10, $xpath->query($modal.'//table[@id="table-riwayat-pranota"]/thead/tr/th')->length);
        $this->assertSame(3, $xpath->query($modal.'//div[contains(concat(" ",normalize-space(@class)," ")," olh-stat ")]')->length);
        $this->assertStringContainsString('Rp 3.185.000', $xpath->query($modal)->item(0)->textContent);
        $this->assertStringContainsString('3 Data Karyawan', $xpath->query($modal)->item(0)->textContent);
        $this->assertSame(3, $xpath->query($modal.'//button[starts-with(@onclick,"toggleKaryawanRow(")]')->length);
        $this->assertSame(3, $xpath->query($modal.'//a[@title="Lihat Rincian"]')->length);
        $locked = $modal.'//tr[@data-nomor="pml-test-102"]';
        $unlocked = $modal.'//tr[@data-nomor="pml-test-101"]';
        $this->assertSame(1, $xpath->query($locked.'//button[@disabled]')->length);
        $this->assertSame(0, $xpath->query($locked.'//button[@title="Edit Pranota"]')->length);
        $this->assertSame(1, $xpath->query($unlocked.'//button[@title="Tambah Karyawan ke Pranota"]')->length);
        $this->assertSame(1, $xpath->query($unlocked.'//button[@title="Edit Pranota"]')->length);
        $this->assertSame(3, $xpath->query($modal.'//form')->length);
        $this->assertStringContainsString('confirm(', $xpath->query($modal.'//form')->item(0)->getAttribute('onsubmit'));
        $this->assertSame(route('pranota-lembur-karyawan.show', 101), $xpath->query($unlocked.'//a[@title="Lihat Rincian"]')->item(0)->getAttribute('href'));
        $this->assertSame(route('pranota-lembur-karyawan.export', 101), $xpath->query($unlocked.'//a[@title="Export Excel"]')->item(0)->getAttribute('href'));
        $this->assertSame($creator->name, $xpath->query($unlocked.'//div[contains(@class,"olh-creator")]')->item(0)->getAttribute('title'));
        $this->assertStringContainsString('Rp -5.000', $xpath->query($modal)->item(0)->textContent);
        $this->assertStringContainsString('Karyawan Satu', $xpath->query($modal.'//*[@id="karyawan-row-101"]')->item(0)->textContent);
        $this->assertSame('filterRiwayatPranota()', $xpath->query('//*[@id="riwayat-search-input"]')->item(0)->getAttribute('oninput'));
        $this->assertStringContainsString('css/payroll-overtime-history.css?v=', $html);
    }
}
