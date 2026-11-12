<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class AbsensiRekapViewTest extends TestCase
{
    private function render(array $parameters = [], bool $empty = false): array
    {
        $request = Request::create('/absensi/rekap', 'GET', $parameters);
        $this->app->instance('request', $request);
        view()->share('errors', new \Illuminate\Support\ViewErrorBag);
        $people = $empty ? collect() : collect([
            (new Karyawan)->forceFill(['id' => 1, 'nik' => '0233', 'nama_lengkap' => 'ACENG JAELANI', 'departemen' => 'JAKARTA PELABUHAN']),
            (new Karyawan)->forceFill(['id' => 2, 'nik' => '1676', 'nama_lengkap' => 'AF TIAR', 'departemen' => null]),
        ]);
        $stats = [
            'total_masuk' => 9, 'sakit' => 1, 'izin' => 2, 'cuti' => 3, 'alpha' => 1,
            'terlambat_kali' => 4, 'terlambat_menit' => 358,
            'pulang_cepat_kali' => 1, 'pulang_cepat_menit' => 331,
            'lembur_jam' => 4, 'lembur_kali' => 2,
            'tidak_absen_masuk_kali' => 1, 'tidak_absen_pulang_kali' => 7, 'tidak_absen_istirahat_kali' => 9,
        ];
        foreach (['hadir', 'terlambat', 'pulang_cepat', 'lembur', 'tidak_absen_masuk', 'tidak_absen_pulang', 'tidak_absen_istirahat', 'sakit', 'izin', 'cuti', 'alpha'] as $detail) {
            $stats['detail_'.$detail] = [['tanggal' => '02/11/2026', 'keterangan' => 'Data detail asli']];
        }
        $html = view('absensi.rekap', [
            'karyawans' => new LengthAwarePaginator($people, $people->count(), 20),
            'allKaryawans' => $people, 'rekapData' => [1 => $stats],
            'startDateStr' => '2026-11-01', 'endDateStr' => '2026-11-12', 'normalWorkdays' => 10,
            'grupsList' => ['UANG MAKAN'], 'grupMap' => [], 'departemens' => ['JAKARTA PELABUHAN'],
            'cabangs' => ['JAKARTA'], 'penempatans' => ['Pelabuhan'], 'pekerjaans' => [], 'divisis' => [],
            'grupsBpjsList' => [], 'grupBpjsMap' => [], 'allHariLiburs' => collect(),
        ])->render();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);

        return [$html, new \DOMXPath($dom)];
    }

    public function test_grouped_header_covers_every_column_and_sticky_hooks_align_with_rows(): void
    {
        [$html, $xpath] = $this->render();
        $head = '//table[contains(@class,"ar-table")]/thead';
        $this->assertSame(2, $xpath->query($head.'/tr')->length);
        $groups = $xpath->query($head.'/tr[1]/th');
        $this->assertSame(5, $groups->length);
        $this->assertSame('2', $groups->item(0)->getAttribute('rowspan'));
        $this->assertSame(16, array_sum(array_map(fn ($cell) => (int) ($cell->getAttribute('colspan') ?: 1), iterator_to_array($groups))));
        $this->assertSame(15, $xpath->query($head.'/tr[2]/th')->length);
        $this->assertSame(16, $xpath->query('//table[contains(@class,"ar-table")]/tbody/tr[1]/td')->length);
        foreach (['ar-col-select', 'ar-col-number', 'ar-col-nik', 'ar-col-name'] as $class) {
            $this->assertSame(1, $xpath->query($head.'//th[contains(@class,"'.$class.'")]')->length);
            $this->assertSame(2, $xpath->query('//table[contains(@class,"ar-table")]/tbody/tr/td[contains(@class,"'.$class.'")]')->length);
        }
        $this->assertStringContainsString('css/absensi-rekap.css?v=', $html);
    }

    public function test_all_values_units_and_detail_payloads_are_preserved(): void
    {
        [, $xpath] = $this->render();
        $row = $xpath->query('//table[contains(@class,"ar-table")]/tbody/tr')->item(0);
        $text = preg_replace('/\s+/u', ' ', $row->textContent);
        foreach (['0233', 'ACENG JAELANI', 'JAKARTA PELABUHAN', '9 Hari', '4x (358m)', '1x (331m)', '2x (4 Jam)', '7x', '9x', '1 Hari', '2 Hari', '3 Hari'] as $value) {
            $this->assertStringContainsString($value, $text);
        }
        foreach (['hadir', 'terlambat', 'pulang-cepat', 'lembur', 'tdk-masuk', 'tdk-pulang', 'tdk-istirahat', 'sakit', 'izin', 'cuti', 'alpha'] as $type) {
            $button = $xpath->query('//table[contains(@class,"ar-table")]/tbody/tr[1]//button[contains(@class,"btn-detail-'.$type.'")]')->item(0);
            $this->assertNotNull($button);
            $this->assertSame('ACENG JAELANI', $button->getAttribute('data-nama'));
            $this->assertSame('02/11/2026', json_decode($button->getAttribute('data-dates'), true)[0]['tanggal']);
        }
    }

    public function test_filters_selection_exports_and_header_actions_keep_their_bindings(): void
    {
        [, $xpath] = $this->render([
            'search' => 'ACENG', 'grup' => 'UANG MAKAN', 'sub_grup' => 'JAKARTA PELABUHAN',
            'cabang' => 'JAKARTA', 'kehadiran' => 'terlambat', 'status_karyawan' => 'berhenti',
            'start_date' => '2026-11-01', 'end_date' => '2026-11-12', 'selected_karyawan' => [1],
        ]);
        foreach (['search', 'grup', 'sub_grup', 'cabang', 'kehadiran', 'status_karyawan', 'start_date', 'end_date'] as $name) {
            $this->assertSame(1, $xpath->query('//*[@id="filterForm"]//*[@name="'.$name.'"]')->length);
        }
        $this->assertSame(route('absensi.rekap'), $xpath->query('//*[@id="filterForm"]')->item(0)->getAttribute('action'));
        $checkbox = $xpath->query('//input[@class and @name="selected_karyawan[]" and @value="1"]')->item(0);
        $this->assertTrue($checkbox->hasAttribute('checked'));
        $this->assertSame('filterForm', $checkbox->getAttribute('form'));
        $quick = '//div[contains(@class,"ar-quick-filters")]';
        $this->assertSame(6, $xpath->query($quick.'//button')->length);
        $active = $xpath->query($quick.'//button[@aria-pressed="true"]');
        $this->assertSame(1, $active->length);
        $this->assertSame("setKehadiranFilter('terlambat')", $active->item(0)->getAttribute('onclick'));
        foreach (['pdf', 'izin_excel', 'terlambat_excel'] as $format) {
            $this->assertSame(1, $xpath->query('//button[@form="filterForm" and @name="export" and @value="'.$format.'"]')->length);
        }
        foreach (['eksporRekapExcel()', 'openIzinModal()', 'openHariLiburModal()'] as $action) {
            $this->assertSame(1, $xpath->query('//button[@onclick="'.$action.'"]')->length);
        }
        $this->assertSame(1, $xpath->query('//a[@href="'.route('absensi.index').'"]')->length);
    }

    public function test_empty_state_spans_all_sixteen_columns(): void
    {
        [$html, $xpath] = $this->render([], true);
        $this->assertStringContainsString('Tidak ada data karyawan', $html);
        $this->assertSame('16', $xpath->query('//table[contains(@class,"ar-table")]/tbody/tr/td')->item(0)->getAttribute('colspan'));
    }
}
