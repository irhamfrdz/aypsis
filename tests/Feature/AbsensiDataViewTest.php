<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AbsensiDataViewTest extends TestCase
{
    private bool $allowActions = true;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn (?User $user) => $this->allowActions);
    }

    private function render(array $parameters = [], bool $empty = false): array
    {
        $this->app->instance('request', Request::create('/absensi', 'GET', $parameters));
        view()->share('errors', new \Illuminate\Support\ViewErrorBag);
        $employee = (new Karyawan)->forceFill(['id' => 1, 'nik' => '1593', 'nama_lengkap' => 'ADITYA NUR RAHMAT KAYA', 'pekerjaan' => 'IT']);
        $full = (new Absensi)->forceFill([
            'nik' => '1593', 'tanggal' => '2026-11-09', 'waktu_masuk' => '2026-11-09 08:58:00',
            'waktu_istirahat_keluar' => '2026-11-09 12:00:00', 'waktu_istirahat_masuk' => '2026-11-09 13:00:00',
            'waktu_pulang' => '2026-11-09 17:22:00', 'waktu_lembur_masuk' => '2026-11-09 20:00:00',
            'waktu_lembur_pulang' => '2026-11-10 02:30:00',
        ])->setRelation('karyawan', $employee);
        $missing = (new Absensi)->forceFill(['nik' => '9999', 'tanggal' => '2026-11-10'])->setRelation('karyawan', null);
        $rows = $empty ? collect() : collect([$full, $missing]);
        $html = view('absensi.index', [
            'absensis' => new LengthAwarePaginator($rows, $rows->count(), 25),
            'pekerjaans' => ['IT'], 'divisis' => [], 'departemens' => ['JAKARTA KANTOR'], 'cabangs' => ['JAKARTA'],
            'startDate' => '2026-11-09', 'endDate' => '2026-11-11',
            'mesins' => collect(), 'karyawanList' => collect([$employee]), 'nonKaryawanList' => collect(),
        ])->render();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);

        return [$html, new \DOMXPath($dom)];
    }

    public function test_all_columns_original_times_and_overnight_duration_are_preserved(): void
    {
        [$html, $xpath] = $this->render();
        $table = '//table[contains(@class,"al-table")]';
        $this->assertSame(13, $xpath->query($table.'/thead/tr/th')->length);
        $this->assertSame(13, $xpath->query($table.'/tbody/tr[1]/td')->length);
        foreach (['08:58', '12:00', '13:00', '17:22', '20:00', '02:30', '6 jam 30 menit'] as $index => $value) {
            $this->assertSame($value, trim($xpath->query($table.'/tbody/tr[1]//span[contains(concat(" ",normalize-space(@class)," ")," al-time ")]')->item($index)->textContent));
        }
        $this->assertStringContainsString('10/11/2026', $html);
        $this->assertStringContainsString('09-11-2026', $html);
        $this->assertStringContainsString('ADITYA NUR RAHMAT KAYA', $html);
        $this->assertStringContainsString('Karyawan Tidak Terdaftar', $html);
        $this->assertSame(6, $xpath->query($table.'/tbody/tr[2]//span[@class="al-time-empty"]')->length);
        $this->assertStringContainsString('css/absensi-data.css?v=', $html);
    }

    public function test_header_actions_import_forms_and_export_filters_keep_their_bindings(): void
    {
        $parameters = ['search' => '1593', 'pekerjaan' => 'IT', 'departemen' => 'JAKARTA KANTOR', 'cabang' => 'JAKARTA', 'start_date' => '2026-11-09', 'end_date' => '2026-11-11'];
        [, $xpath] = $this->render($parameters);
        $header = '//div[@class="al-header-actions"]';
        $this->assertSame(8, $xpath->query($header.'/*')->length);
        foreach (['absensi.export_excel', 'absensi.export_pdf'] as $route) {
            $this->assertSame(1, $xpath->query($header.'/a[@href="'.route($route, $parameters).'"]')->length);
        }
        foreach (['createModal', 'importModal', 'importKoreksiModal', 'exportDatModal'] as $id) {
            $this->assertSame(1, $xpath->query('//*[@id="'.$id.'"]')->length);
            $this->assertSame(1, $xpath->query($header.'/button[contains(@onclick,"'.$id.'")]')->length);
        }
        foreach (['importModal' => 'absensi.import', 'importKoreksiModal' => 'absensi.import_excel_koreksi', 'createModal' => 'absensi.store', 'exportDatModal' => 'absensi.export_dat'] as $id => $route) {
            $this->assertSame(route($route), $xpath->query('//*[@id="'.$id.'"]//form')->item(0)->getAttribute('action'));
        }
        $this->assertSame(1, $xpath->query($header.'/a[@href="'.route('absensi.rekap').'"]')->length);
        $this->assertSame(1, $xpath->query($header.'/a[@href="'.route('absensi.export_machine_users').'"]')->length);
        $filter = '//div[contains(@class,"al-filters")]/form';
        $this->assertSame(route('absensi.index'), $xpath->query($filter)->item(0)->getAttribute('action'));
        foreach (['search', 'start_date', 'end_date'] as $name) {
            $this->assertSame($parameters[$name], $xpath->query($filter.'//input[@name="'.$name.'"]')->item(0)->getAttribute('value'));
        }
        foreach (['pekerjaan', 'departemen', 'cabang'] as $name) {
            $this->assertSame($parameters[$name], $xpath->query($filter.'//select[@name="'.$name.'"]/option[@selected]')->item(0)->getAttribute('value'));
        }
        $this->assertSame(1, $xpath->query($filter.'//a[@href="'.route('absensi.index').'"]')->length);
    }

    public function test_detail_edit_delete_and_individual_scan_deletion_are_preserved(): void
    {
        [, $xpath] = $this->render();
        $actions = '//div[contains(@class,"al-row-actions")]';
        $detail = $xpath->query($actions.'/a[@title="Lihat Detail"]')->item(0);
        $this->assertSame(route('absensi.show', ['nik' => '1593', 'tanggal' => '2026-11-09']), $detail->getAttribute('href'));
        $edit = $xpath->query($actions.'/button[@title="Edit Absensi"]')->item(0);
        $this->assertStringContainsString("openEditModal('1593', '2026-11-09', '08:58'", $edit->getAttribute('onclick'));
        $form = $xpath->query($actions.'/form')->item(0);
        $this->assertSame(route('absensi.destroy_day'), $form->getAttribute('action'));
        $this->assertStringContainsString('confirm(', $form->getAttribute('onsubmit'));
        $scanForms = $xpath->query('//table[contains(@class,"al-table")]//form[@action="'.route('absensi.delete_log').'"]');
        $this->assertSame(4, $scanForms->length);
        foreach ($scanForms as $scan) {
            $this->assertStringContainsString('confirm(', $scan->getAttribute('onsubmit'));
        }
        $this->allowActions = false;
        [, $xpath] = $this->render();
        $this->assertSame(0, $xpath->query($actions.'/button[@title="Edit Absensi"]')->length);
        $this->assertSame(0, $xpath->query($actions.'//button[@title="Hapus Absensi"]')->length);
        $this->assertSame(7, $xpath->query('//div[@class="al-header-actions"]/*')->length);
    }

    public function test_empty_state_keeps_thirteen_columns(): void
    {
        [$html, $xpath] = $this->render([], true);
        $this->assertStringContainsString('Tidak ada data absensi', $html);
        $this->assertSame('13', $xpath->query('//table[contains(@class,"al-table")]/tbody/tr/td')->item(0)->getAttribute('colspan'));
    }
}
