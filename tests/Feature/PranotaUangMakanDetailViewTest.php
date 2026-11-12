<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\PranotaUangMakan;
use App\Models\PranotaUangMakanDetail;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PranotaUangMakanDetailViewTest extends TestCase
{
    private function pranota(): PranotaUangMakan
    {
        $details = collect([
            ['6 Hari', 330000, 0, 330000],
            ['3', 290000, 10000, 300000],
            ['4 Hari Hari', 265000, -5000, 260000],
        ])->map(function ($values, $index) {
            [$attendance, $initial, $adjustment, $total] = $values;
            $employee = (new Karyawan)->forceFill(['nama_lengkap' => 'Karyawan Contoh '.$index, 'nik' => '000'.$index]);
            return (new PranotaUangMakanDetail)->forceFill([
                'kehadiran' => $attendance, 'nominal_awal' => $initial,
                'adjustment' => $adjustment, 'total_akhir' => $total,
                'catatan' => $index === 0 ? "Catatan lengkap <penting>\nBaris kedua tidak boleh terpotong." : null,
            ])->setRelation('karyawan', $employee);
        });
        return (new PranotaUangMakan)->forceFill([
            'id' => 88, 'nomor_pranota' => 'PUM-26-11-001',
            'tanggal_pranota' => '2026-11-09', 'total_nominal' => 890000,
        ])->setRelation('details', $details);
    }

    private function render(PranotaUangMakan $pranota): array
    {
        $html = view('pranota-uang-makan.show', compact('pranota'))->render();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);

        return [$html, new \DOMXPath($dom)];
    }

    public function test_summary_and_rows_use_existing_details_without_extra_database_queries(): void
    {
        $pranota = $this->pranota();
        DB::enableQueryLog();
        DB::flushQueryLog();
        [$html, $xpath] = $this->render($pranota);
        $this->assertCount(0, DB::getQueryLog());
        DB::disableQueryLog();
        $this->assertSame('3 Orang', trim($xpath->query('//*[@id="pmd-employee-count"]')->item(0)->textContent));
        $this->assertSame('13 Hari', trim($xpath->query('//*[@id="pmd-attendance"]')->item(0)->textContent));
        $this->assertSame('Rp 890.000', trim($xpath->query('//*[@id="pmd-total"]')->item(0)->textContent));
        $this->assertSame(3, $xpath->query('//table[contains(@class,"pmd-table")]/tbody/tr')->length);
        $this->assertSame(7, $xpath->query('//table[contains(@class,"pmd-table")]/thead/tr/th')->length);
        foreach (['6 Hari', '3 Hari', '4 Hari'] as $index => $attendance) {
            $this->assertSame($attendance, trim($xpath->query('//span[@class="pmd-attendance-badge"]')->item($index)->textContent));
        }
        $this->assertSame('3', trim($xpath->query('//td[@class="pmd-row-number"]')->item(2)->textContent));
        $this->assertStringNotContainsString('Hari Hari', $html);
        $this->assertStringContainsString('09/11/2026', $html);
        $this->assertStringContainsString('css/pranota-uang-makan-detail.css?v=', $html);
    }

    public function test_adjustments_notes_and_existing_actions_are_preserved(): void
    {
        [$html, $xpath] = $this->render($this->pranota());
        $this->assertSame('+ Rp 10.000', trim($xpath->query('//td[contains(@class,"pmd-positive")]')->item(0)->textContent));
        $this->assertSame('− Rp 5.000', trim($xpath->query('//td[contains(@class,"pmd-negative")]')->item(0)->textContent));
        $this->assertSame('Rp 0', trim($xpath->query('//td[contains(@class,"pmd-zero")]')->item(0)->textContent));
        $this->assertStringContainsString('Catatan lengkap &lt;penting&gt;', $html);
        $this->assertStringContainsString('Baris kedua tidak boleh terpotong.', $html);
        $this->assertSame('-', trim($xpath->query('//td[@class="pmd-notes"]')->item(1)->textContent));
        $this->assertSame(route('pranota-uang-makan.edit', 88), $xpath->query('//a[contains(@class,"pmd-button-primary")]')->item(0)->getAttribute('href'));
        $form = $xpath->query('//div[@class="pmd-actions"]/form')->item(0);
        $this->assertSame(route('pranota-uang-makan.destroy', 88), $form->getAttribute('action'));
        $this->assertStringContainsString('confirm(', $form->getAttribute('onsubmit'));
        $this->assertSame('DELETE', $xpath->query('//input[@name="_method"]')->item(0)->getAttribute('value'));
        $this->assertSame(1, $xpath->query('//button[@onclick="window.print()"]')->length);
        $this->assertSame('Rp 890.000', trim($xpath->query('//tfoot/tr/td[last()]')->item(0)->textContent));
    }

    public function test_empty_details_and_missing_employee_render_safely(): void
    {
        $pranota = $this->pranota();
        $pranota->details->first()->setRelation('karyawan', null);
        [$html] = $this->render($pranota);
        $this->assertStringContainsString('class="pmd-avatar" aria-hidden="true">U</span>', $html);
        $pranota->setRelation('details', collect());
        [$html, $xpath] = $this->render($pranota);
        $this->assertStringContainsString('Tidak ada rincian data karyawan.', $html);
        $this->assertSame('0 Orang', trim($xpath->query('//*[@id="pmd-employee-count"]')->item(0)->textContent));
        $this->assertSame('0 Hari', trim($xpath->query('//*[@id="pmd-attendance"]')->item(0)->textContent));
        $this->assertSame('Rp 0', trim($xpath->query('//*[@id="pmd-total"]')->item(0)->textContent));
        $this->assertSame('7', $xpath->query('//td[@class="pmd-empty"]')->item(0)->getAttribute('colspan'));
    }
}
