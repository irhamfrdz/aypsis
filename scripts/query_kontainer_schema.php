<?php
$db=new PDO('sqlite:C:\\folder_kerjaan\\aypsis\\database\\database.sqlite');
foreach($db->query("PRAGMA table_info('kontainers')") as $r){
    echo $r['name'] . "\t" . $r['type'] . "\n";
}
