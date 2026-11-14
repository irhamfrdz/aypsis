<?php

namespace Tests\Feature;

use App\Models\PranotaBpjsHeader;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class PranotaBpjsSharedFormTest extends TestCase
{
    public function test_create_and_edit_share_columns_controls_and_keep_edit_values(): void
    {
        Gate::before(fn () => true);
        $layout = tempnam(sys_get_temp_dir(), 'bpjs-layout-');
        file_put_contents($layout, "@yield('content')\n@stack('styles')");
        View::composer('layouts.app', fn ($view) => $view->setPath($layout));
        $header = new PranotaBpjsHeader(['nomor_pranota' => 'TEST-EDIT', 'tanggal_pranota' => '2026-10-15',
            'periode_bulan' => 10, 'periode_tahun' => 2026, 'status' => 'partial', 'keterangan' => 'Catatan tersimpan']);
        $header->id = 123;
        $header->setRelation('details', collect());
        $data = ['karyawans' => collect(), 'rumusBpjs' => collect(), 'refreshPranota' => null, 'errors' => new \Illuminate\Support\ViewErrorBag];
        try {
            $create = view('pranota-bpjs.create', $data)->render();
            view()->flushState();
            $edit = view('pranota-bpjs.edit', $data + ['pranota_bpj' => $header])->render();
            foreach (['filter-group-bpjs', 'filter-cabang-bpjs', 'search-karyawan', 'btn-refresh-bpjs',
                'btn-generate-all', 'btn-add-karyawan', 'tabel-detail', 'bpjs-journal-title'] as $id) {
                $this->assertStringContainsString('id="'.$id.'"', $create);
                $this->assertStringContainsString('id="'.$id.'"', $edit);
            }
            $this->assertStringContainsString('value="PUT"', $edit);
            $this->assertStringContainsString(route('pranota-bpjs.update', 123), $edit);
            $this->assertStringContainsString('value="2026-10-15"', $edit);
            $this->assertStringContainsString('Catatan tersimpan', $edit);
            $this->assertStringContainsString('Simpan Perubahan', $edit);
            $this->assertStringContainsString('Pranota sudah dibayar sebagian', $edit);
            $this->assertStringNotContainsString('value="PUT"', $create);
            $domCreate = new \DOMDocument;
            $domEdit = new \DOMDocument;
            @$domCreate->loadHTML($create);
            @$domEdit->loadHTML($edit);
            $headers = fn ($dom) => array_map(fn ($node) => trim($node->textContent), iterator_to_array((new \DOMXPath($dom))->query('//table[@id="tabel-detail"]/thead//th')));
            $this->assertSame($headers($domCreate), $headers($domEdit));
        } finally {
            unlink($layout);
        }
    }
}
