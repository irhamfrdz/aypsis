<?php
$content = file_get_contents('app/Models/TandaTerimaLcl.php');
$content = str_replace('class TandaTerimaLcl ', 'class TandaTerimaLclBatam ', $content);
$content = str_replace("'tanda_terima_lcl'", "'tanda_terima_lcl_batams'", $content);
$content = preg_replace('/public function items\(\).*?\{.*?\}/s', 
    'public function items() { return $this->hasMany(TandaTerimaLclBatamItem::class, \'tanda_terima_lcl_batam_id\'); }', 
    $content);
$content = preg_replace('/public function kontainerPivot\(\).*?\{.*?\}/s', 
    'public function kontainerPivot() { return $this->hasMany(TtLclBatamKontainerPivot::class, \'tt_lcl_batam_id\'); }', 
    $content);
file_put_contents('app/Models/TandaTerimaLclBatam.php', $content);
echo 'Model created';
