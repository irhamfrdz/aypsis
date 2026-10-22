<?php

namespace Tests\Unit;

use Tests\TestCase;

class BiayaKapalBuruhEditFieldsTest extends TestCase
{
    public function test_jakarta_buruh_edit_exposes_and_restores_payment_fields(): void
    {
        $sectionScript = file_get_contents(resource_path('views/biaya-kapal/edit/_js-kapal-sections.blade.php'));
        $initScript = file_get_contents(resource_path('views/biaya-kapal/edit/_js-edit-init.blade.php'));

        foreach (['nama_vendor', 'penerima', 'bank_id', 'nomor_rekening'] as $field) {
            $this->assertStringContainsString("kapal_sections[\${sectionIndex}][{$field}]", $sectionScript);
        }

        $this->assertStringContainsString("'nama_vendor' => \$batamItem ? '' : (\$biayaKapal->nama_vendor ?? '')", $initScript);
        $this->assertStringContainsString("restoreEditSelectValue(section.querySelector('.penerima-select'), myData.penerima)", $initScript);
        $this->assertStringContainsString("restoreEditSelectValue(section.querySelector('.bank-select'), myData.bank_id)", $initScript);
        $this->assertStringContainsString("nomorRekeningInput.value = myData.nomor_rekening || ''", $initScript);
    }
}
